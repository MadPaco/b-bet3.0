import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';
import { fetchThreeLatestUserAchievement } from '../../utility/api';
import { useEffect, useState } from 'react';

interface Achievement {
  name: string;
  image: string;
  flavorText: string;
  dateEarned: {
    date: string;
  };
}

interface AchievementPanelProps { }

const AchievementPanel: React.FC<AchievementPanelProps> = () => {
  const { username } = useAuth();
  const [latestAchievements, setLatestAchievements] = useState<Achievement[]>([]);

  useEffect(() => {
    const fetchLatestAchievements = async () => {
      if (username) {
        const response = await fetchThreeLatestUserAchievement(username);
        if (response.status === 200) {
          const data = await response.json();
          setLatestAchievements(data);
        } else {
          setLatestAchievements([]);
        }
      }
    };
    fetchLatestAchievements();
  }, [username]);

  return (
    <Panel>
      {username ? (
        <div className="flex items-center flex-col">
          <h2 className="text-xl font-semibold mb-2">Latest Achievements</h2>
          <div className="flex items-center justify-center flex-wrap p-3">
            {latestAchievements.length > 0 ? (
              latestAchievements.map((achievement, index) => (
                <div key={index} className="lg:w-1/4 text-center bg-gray-700 p-3 rounded-xl shadow-inner shadow-white flex flex-col m-2 items-center">
                  <p>{achievement.name}</p>
                  <img className="lg:w-3/4 m-2" src={`/assets/images/achievements/${achievement.image}`} alt={achievement.name} />
                  <p>{achievement.flavorText}</p>
                  <p>{achievement.dateEarned.date}</p>
                </div>
              ))
            ) : (
              <p>No achievements found</p>
            )}
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )}
    </Panel>
  );
};

export default AchievementPanel;

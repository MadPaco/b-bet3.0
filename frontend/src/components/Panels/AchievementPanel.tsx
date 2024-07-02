import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';
import { fetchLatestUserAchievement } from '../../utility/api';
import { useEffect, useState } from 'react';

interface AchievementPanelProps { }




const AchievementPanel: React.FC<AchievementPanelProps> = () => {
  const { username } = useAuth();
  const [latestAchievement, setLatestAchievement] = useState<Achievement | null>(null);

  useEffect(() => {
    const fetchLatestAchievement = async () => {
      const response = await fetchLatestUserAchievement(username);
      if (response.status === 200) {
        const data = await response.json();
        setLatestAchievement(data);
      }
    }
    if (username) {
      fetchLatestAchievement();
    }
  }, []);
  return (
    <Panel>
      {username ? (
        <div className="flex items-center flex-col">
          <h2 className='text-xl font-semibold mb-2' >Latest Achievement</h2>
          <div className="flex items-center">
            {latestAchievement ? (
              <div className='lg:w-1/3 text-center bg-gray-700 p-3 rounded-xl shadow-inner shadow-white flex items-center flex-col' >
                <p>{latestAchievement.name}</p>
                <img className='lg:w-3/4 m-2' src={`/assets/images/achievements/${latestAchievement.image}`}></img>
                <p>{latestAchievement.flavorText}</p>
                <p>{latestAchievement.dateEarned.date}</p>
              </div>) : null}
          </div>
        </div>
      ) : (
        <p>Loading...</p>
      )
      }
    </Panel >
  );
};

export default AchievementPanel;

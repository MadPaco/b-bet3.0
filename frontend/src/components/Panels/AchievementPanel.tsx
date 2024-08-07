import React, { useEffect, useState, useRef } from 'react';
import Panel from '../common/Panel';
import { useAuth } from '../auth/AuthContext';
import { fetchThreeLatestUserAchievement } from '../../utility/api';

interface Achievement {
  name: string;
  image: string;
  flavorText: string;
  dateEarned: string;
  description: string;
}

interface AchievementPanelProps { }

const AchievementPanel: React.FC<AchievementPanelProps> = () => {
  const { username } = useAuth();
  const [latestAchievements, setLatestAchievements] = useState<Achievement[]>([]);
  const descriptionRefs = useRef<Array<HTMLParagraphElement | null>>([]);

  useEffect(() => {
    const fetchLatestAchievements = async () => {
      if (username) {
        try {
          const achievementData = await fetchThreeLatestUserAchievement(username);
          setLatestAchievements(achievementData);
        }
        catch (error) {
          setLatestAchievements([]);
          console.error('Failed to fetch achievements', error)
        }
      }
    };
    fetchLatestAchievements();
  }, [username]);

  useEffect(() => {
    descriptionRefs.current.forEach((desc) => {
      if (desc) {
        let fontSize = parseInt(window.getComputedStyle(desc).fontSize);
        while (desc.scrollHeight > desc.clientHeight && fontSize > 12) {
          fontSize -= 1;
          desc.style.fontSize = `${fontSize}px`;
        }
      }
    });
  }, [latestAchievements]);

  return (
    <Panel>
      {username ? (
        <div className="flex items-center flex-col">
          <h2 className="text-xl font-semibold mb-2">Latest Achievements</h2>
          <div className="flex items-center justify-around flex-wrap p-3">
            {latestAchievements.length > 0 ? (
              latestAchievements.map((achievement, index) => (
                <div
                  key={index}
                  className="xl:w-1/4 text-center bg-gray-700 p-3 rounded-xl shadow-inner shadow-white flex flex-col m-2 items-center"
                  style={{ minHeight: '400px' }} // Ensuring minimum height
                >
                  <p className="font-bold">{achievement.name}</p>
                  <div className="w-full h-48 flex items-center justify-center overflow-hidden">
                    <img
                      className="w-full h-full object-cover object-top rounded-md"
                      src={`/assets/images/achievements/${achievement.image}`}
                      alt={achievement.name}
                    />
                  </div>
                  <div className="h-24 flex items-center justify-center overflow-hidden italic">
                    <p>{achievement.flavorText}</p>
                  </div>
                  <p
                    ref={(el) => (descriptionRefs.current[index] = el)}
                    className="text-sm mb-3 text-gray-400 overflow-hidden"
                    style={{ maxHeight: '3em' }}
                  >
                    {achievement.description}
                  </p>
                  <p className="text-sm text-gray-400">{achievement.dateEarned}</p>
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

import React from 'react';
import TeamInfoPanel from '../components/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/Panels/UserInfoPanel';
import UpcomingGamesPanel from '../components/Panels/UpcomingGamesPanel';
import AchievementPanel from '../components/Panels/AchievementPanel';
import ChatPanel from '../components/Panels/ChatPanel';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import { fetchFavTeamBanner } from '../utility/api'
import { useState, useEffect } from 'react';
import { useAuth } from '../components/auth/AuthContext';

const Dashboard: React.FC = () => {
  const [banner, setBanner] = useState<string>('nflLogo.webp')
  const { username } = useAuth();

  useEffect(() => {
    const fetchBanner = async () => {
      try {
        const bannerData = await fetchFavTeamBanner(username);
        setBanner(bannerData['banner']);
      }
      catch (err) {
        console.log(err)
      }
    }
    fetchBanner();
  }, [username])

  return (
    <LoggedInLayout>
      <div className="items-center h-40 justify-center w-full hidden md:flex">
        <img className="object-fill object-center h-full w-full" src={`assets/images/${banner}`} alt="Team Banner"></img>
      </div>

      <div className="flex flex-col 2xl:flex-row 2xl:pt-2">
        {/* Left Column */}
        <div className="flex flex-col 2xl:w-1/2 space-y-6">
          <div className="flex-1">
            <div className='flex flex-wrap 2xl:h-1/3'>
              <div className="w-full 2xl:w-1/2">
                <TeamInfoPanel />
              </div>
              <div className='w-full 2xl:w-1/2'>
                <UserInfoPanel />
              </div>
            </div>
            <AchievementPanel />
          </div>
        </div>

        {/* Right Column */}
        <div className="flex flex-col 2xl:h-1/3 2xl:flex-1">
          <div className="h-1/4">
            <ChatPanel />
          </div>
          <div className="h-1/4 mt-0 mb-4">
            <UpcomingGamesPanel />
          </div>
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default Dashboard;

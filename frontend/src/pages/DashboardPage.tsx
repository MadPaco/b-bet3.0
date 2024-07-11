import React from 'react';
import TeamInfoPanel from '../components/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/Panels/UserInfoPanel';
import UpcomingGamesPanel from '../components/Panels/UpcomingGamesPanel';
import AchievementPanel from '../components/Panels/AchievementPanel';
import ChatPanel from '../components/Panels/ChatPanel';
import DivisionPanel from '../components/Panels/DivisionPanel';
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
        const response = await fetchFavTeamBanner(username);
        const data = await response.json();
        setBanner(data['banner']);
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

      <div className="flex flex-col xl:flex-row xl:space-x-6 xl:pt-2">
        {/* Left Column */}
        <div className="flex flex-col xl:w-1/2 space-y-6">
          <div className="flex-1">
            <div className='flex flex-wrap xl:h-1/3'>
              <div className="w-full xl:w-1/3">
                <TeamInfoPanel />
              </div>
              <div className='w-full xl:w-1/3'>
                <UserInfoPanel />
              </div>
              <div className='w-full xl:w-1/3'>
                <DivisionPanel />
              </div>
            </div>
            <AchievementPanel />
          </div>
        </div>

        {/* Right Column */}
        <div className="flex flex-col xl:h-1/3 xl:flex-1">
          <div className="h-1/4">
            <ChatPanel />
          </div>
          <div className="h-1/4 mt-0">
            <UpcomingGamesPanel />
          </div>
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default Dashboard;

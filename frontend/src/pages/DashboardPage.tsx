import React from 'react';
import TeamInfoPanel from '../components/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/Panels/UserInfoPanel';
import UpcomingGamesPanel from '../components/Panels/UpcomingGamesPanel';
import AchievementPanel from '../components/Panels/AchievementPanel';
import ChatPanel from '../components/Panels/ChatPanel';
import DivisionPanel from '../components/Panels/DivisionPanel';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import '../utility/api';

const Dashboard: React.FC = () => {
  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:flex-row lg:space-x-6 lg:pt-10">
        {/* Left Column */}
        <div className="flex flex-col space-y-6">
          <div className="flex-1">
            <div className='flex flex-wrap lg:h-1/3'>
              <div className="w-full lg:w-1/3">
                <TeamInfoPanel />
              </div>
              <div className='w-full lg:w-1/3'>
                <UserInfoPanel />
              </div>
              <div className='w-full lg:w-1/3'>
                <DivisionPanel />
              </div>
            </div>
            <AchievementPanel />
          </div>
        </div>

        {/* Right Column */}
        <div className="flex flex-col space-y-6 lg:flex-1">
          <div className="flex-1">
            <ChatPanel />
          </div>
          <div className="flex-1">
            <UpcomingGamesPanel />
          </div>
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default Dashboard;

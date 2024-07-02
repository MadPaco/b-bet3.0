import React from 'react';
import TeamInfoPanel from '../components/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/Panels/UserInfoPanel';
import MessageOverviewPanel from '../components/Panels/AchievementPanel';
import ChatPanel from '../components/Panels/ChatPanel';
import DivisionPanel from '../components/Panels/DivisionPanel';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import '../utility/api';

const Dashboard: React.FC = () => {
  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:flex-row lg:space-x-6 lg:pt-10">
        {/* Left Column */}
        <div className="flex flex-col space-y-6 lg:flex-1">
          <div className="flex-1">
            <TeamInfoPanel />
          </div>
          <div className="flex-1">
            <UserInfoPanel />
          </div>
          <div className="flex-1">
            <DivisionPanel />
          </div>
        </div>

        {/* Right Column */}
        <div className="flex flex-col space-y-6 lg:flex-1">
          <div className="flex-1">
            <MessageOverviewPanel />
          </div>
          <div className="flex-1 lg:flex-grow">
            <ChatPanel />
          </div>
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default Dashboard;

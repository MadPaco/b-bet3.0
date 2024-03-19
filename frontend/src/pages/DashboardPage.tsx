import { useAuth } from '../components/auth/AuthContext';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import TeamInfoPanel from '../components/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/Panels/UserInfoPanel';
import MessageOverviewPanel from '../components/Panels/MessageOverviewPanel';
import ChatPanel from '../components/Panels/ChatPanel';
import ActivityPanel from '../components/Panels/ActivityPanel';
import NewsPanel from '../components/Panels/NewsPanel';
import LoggedInLayout from '../components/layout/LoggedInLayout';
import '../utility/api';
import { fetchPrimaryColor } from '../utility/api';

const Dashboard: React.FC = () => {
  const { username, favTeam } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!username) {
      navigate('/login');
    } else {
      (async () => {
        const color = await fetchPrimaryColor(favTeam);
        setPrimaryColor(color);
      })();
    }
  }, [username, navigate, favTeam]);

  return (
    <LoggedInLayout>
      <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3">
        <div className="lg:col-span-1 lg:row-span-1">
          <TeamInfoPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <NewsPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <MessageOverviewPanel />
        </div>
        <div className="lg:col-span-2 lg:row-span-2">
          <ChatPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <UserInfoPanel />
        </div>
        <div className="lg:col-span-1 lg:row-span-1">
          <ActivityPanel />
        </div>
      </div>
    </LoggedInLayout>
  );
};

export default Dashboard;

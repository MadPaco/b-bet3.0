import Layout from '../components/layout/Layout';
import Sidebar from '../components/layout/Sidebar';
import { useAuth } from '../components/auth/AuthContext';
import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import TeamInfoPanel from '../components/layout/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/layout/Panels/UserInfoPanel';
import MessageOverviewPanel from '../components/layout/Panels/MessageOverviewPanel';
import ChatPanel from '../components/layout/Panels/ChatPanel';
import ActivityPanel from '../components/layout/Panels/ActivityPanel';

const Dashboard: React.FC = () => {
  const { username, favTeam } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!username) {
      navigate('/login');
    }
  }, [username, navigate]);

  return (
    <Layout
      content={
        <div className="grid grid-cols-7 h-full w-full">
          <Sidebar />
          <div className="col-span-6 h-full">
            <div className="grid grid-cols-3 grid-rows-3 gap-4 h-full">
              <div className="col-span-1 row-span-1 h-full">
                <TeamInfoPanel />
              </div>
              <div className="col-span-1 row-span-1 h-full">
                <UserInfoPanel />
              </div>
              <div className="col-span-1 row-span-1 h-full">
                <MessageOverviewPanel />
              </div>
              <div className="col-span-2 row-span-2 h-full">
                <ChatPanel />
              </div>
              <div className="col-span-1 row-span-2 h-full">
                <ActivityPanel />
              </div>
            </div>
          </div>
        </div>
      }
    />
  );
};

export default Dashboard;

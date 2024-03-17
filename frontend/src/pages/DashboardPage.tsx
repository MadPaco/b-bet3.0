import Layout from '../components/layout/Layout';
import Sidebar from '../components/layout/Sidebar';
import { useAuth } from '../components/auth/AuthContext';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import TeamInfoPanel from '../components/layout/Panels/TeamInfoPanel';
import UserInfoPanel from '../components/layout/Panels/UserInfoPanel';
import MessageOverviewPanel from '../components/layout/Panels/MessageOverviewPanel';
import ChatPanel from '../components/layout/Panels/ChatPanel';
import ActivityPanel from '../components/layout/Panels/ActivityPanel';
import { fetchTeamInfo } from '../utility/api';

const Dashboard: React.FC = () => {
  const { username, favTeam } = useAuth();
  const navigate = useNavigate();
  const [primaryColor, setPrimaryColor] = useState<string>('gray');

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setPrimaryColor(data.primaryColor))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  useEffect(() => {
    if (!username) {
      navigate('/login');
    }
  }, [username, navigate]);

  return (
    <Layout
      content={
        <div className="grid grid-cols-7 h-full w-full">
          <Sidebar color={primaryColor} />
          <div className="col-span-6 h-full">
            <div className="grid grid-cols-3 grid-rows-3 gap-4 h-full">
              <div className="col-span-1 row-span-1 h-full">
                <TeamInfoPanel color={primaryColor} />
              </div>
              <div className="col-span-1 row-span-1 h-full">
                <UserInfoPanel color={primaryColor} />
              </div>
              <div className="col-span-1 row-span-1 h-full">
                <MessageOverviewPanel color={primaryColor} />
              </div>
              <div className="col-span-2 row-span-2 h-full">
                <ChatPanel color={primaryColor} />
              </div>
              <div className="col-span-1 row-span-2 h-full">
                <ActivityPanel color={primaryColor} />
              </div>
            </div>
          </div>
        </div>
      }
    />
  );
};

export default Dashboard;

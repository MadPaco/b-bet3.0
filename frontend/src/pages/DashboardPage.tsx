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
import NewsPanel from '../components/layout/Panels/NewsPanel';

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
        <div className="flex flex-col lg:grid lg:grid-cols-7 w-full">
          <Sidebar color={primaryColor} />
          <div className="grid col-span-6 ">
            <div className="flex flex-col lg:pt-10 lg:grid lg:grid-cols-3 lg:grid-rows-3">
              <div className="lg:col-span-1 lg:row-span-1">
                <TeamInfoPanel color={primaryColor} />
              </div>
              <div className="lg:col-span-1 lg:row-span-1">
                <NewsPanel color={primaryColor} />
              </div>
              <div className="lg:col-span-1 lg:row-span-1">
                <MessageOverviewPanel color={primaryColor} />
              </div>
              <div className="lg:col-span-2 lg:row-span-2">
                <ChatPanel color={primaryColor} />
              </div>
              <div className="lg:col-span-1 lg:row-span-1">
                <UserInfoPanel color={primaryColor} />
              </div>
              <div className="lg:col-span-1 lg:row-span-1">
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

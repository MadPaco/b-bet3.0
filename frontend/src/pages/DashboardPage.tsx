import { useAuth } from '../components/auth/AuthContext';
import { useNavigate } from 'react-router-dom';
import Layout from '../components/layout/Layout';
import { useEffect, useState } from 'react';
import TeamInfo from '../components/TeamInfo';
import Sidebar from '../components/layout/Sidebar';

const Dashboard: React.FC = () => {
  const { favTeam, username, createdAt } = useAuth();
  const navigate = useNavigate();
  const [teamInfo, setTeamInfo] = useState<{
    logo: string;
    name: string;
    shorthand_name: string;
    location: string;
    division: string;
    conference: string;
  } | null>(null);

  useEffect(() => {
    if (!username) {
      navigate('/login');
    }
  }, [username, navigate]);

  useEffect(() => {
    if (favTeam) {
      fetch(
        `http://127.0.0.1:8000/backend/team?favTeam=${encodeURIComponent(favTeam)}`,
      )
        .then((response) => response.json())
        .then((data) => setTeamInfo(data));
    }
  }, [favTeam]);

  return (
    <div className="flex">
      <Sidebar />
      <div className="flex-grow overflow-auto">
        <Layout
          content={
            <>
              <h1>hello {username}</h1>
              <h1>{favTeam}</h1>
              <h1>{createdAt ? createdAt.toString() : 'Not set'}</h1>
            </>
          }
        />
      </div>
    </div>
  );
};

export default Dashboard;

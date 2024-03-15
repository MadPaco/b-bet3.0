import { useAuth } from '../components/AuthContext';
import { useNavigate } from 'react-router-dom';
import Layout from '../components/Layout';
import { useEffect, useState } from 'react';

const Dashboard: React.FC = () => {
  const { favTeam, username, createdAt } = useAuth();
  const navigate = useNavigate();
  const [teamInfo, setTeamInfo] = useState<{
    logo: string;
    name: string;
    shorthand_name: string;
    location: string;
    division: string;
    conferece: string;
  } | null>(null);

  useEffect(() => {
    if (!username) {
      navigate('/login');
    }
  }, [username, navigate]);

  function getTeamLogoPath(teamAbbreviation: string) {
    return import.meta.env.BASE_URL + 'assets/images/teams/' + teamAbbreviation;
  }

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
    <Layout
      content={
        <div>
          <h1>hello {username}</h1>
          <h1>{favTeam}</h1>
          <h1>{createdAt ? createdAt.toString() : 'Not set'}</h1>
          {teamInfo && (
            <img src={getTeamLogoPath(teamInfo.logo)} alt={teamInfo.name} />
          )}
        </div>
      }
    />
  );
};

export default Dashboard;

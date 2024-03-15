import { useAuth } from '../components/AuthContext';
import { useNavigate } from 'react-router-dom';
import Layout from '../components/Layout';
import { useEffect } from 'react';

const Dashboard: React.FC = () => {
  const { favTeam, username, createdAt } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!username) {
      navigate('/login');
    }
  }, [username, navigate]);

  return (
    <Layout
      content={
        <div>
          <h1>hello {username}</h1>
          <h1>{favTeam}</h1>
          <h1>{createdAt ? createdAt.toString() : 'Not set'}</h1>
        </div>
      }
    />
  );
};

export default Dashboard;

import Layout from '../components/layout/Layout';
import Sidebar from '../components/layout/Sidebar';
import { useAuth } from '../components/auth/AuthContext';
import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

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
        <div className="grid grid-cols-7 w-full">
          <Sidebar />
          <div className="col-span-6">
            <h1>hello {username}</h1>
            <h1>{favTeam}</h1>
            <h1>{createdAt ? createdAt.toString() : 'Not set'}</h1>
          </div>
        </div>
      }
    />
  );
};

export default Dashboard;

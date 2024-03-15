import { useAuth } from '../components/AuthContext';
import Layout from '../components/Layout';

const Dashboard: React.FC = () => {
  const { favTeam, createdAt } = useAuth();

  return (
    <Layout
      content={
        <div>
          <h1>{favTeam}</h1>
          <h1>{createdAt ? createdAt.toString() : 'Not set'}</h1>
        </div>
      }
    />
  );
};

export default Dashboard;

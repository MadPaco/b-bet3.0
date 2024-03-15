import { useAuth } from '../components/AuthContext';

const Dashboard: React.FC = () => {
  const { favTeam, createdAt } = useAuth();

  return (
    <div>
      <h1>{favTeam}</h1>
      <h1>{createdAt ? createdAt.toString() : 'Not set'}</h1>
    </div>
  );
};

export default Dashboard;

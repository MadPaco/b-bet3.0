import { useAuth } from '../components/AuthContext';

const Dashboard: React.FC = () => {
  const { favTeam } = useAuth();

  return (
    <div>
      <h1>{favTeam}</h1>
    </div>
  );
};

export default Dashboard;

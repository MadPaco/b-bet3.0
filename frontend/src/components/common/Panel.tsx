import { useEffect, useState } from 'react';
import { useAuth } from '../auth/AuthContext';
import { fetchTeamInfo } from '../../utility/api';
import { colorClasses } from '../../data/colorClasses';

interface PanelProps {
  children: React.ReactNode;
}

const Panel: React.FC<PanelProps> = ({ children }) => {
  const { favTeam } = useAuth();
  const [primaryColor, setPrimaryColor] = useState('');

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setPrimaryColor(data.primaryColor))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <div
      className={`flex flex-col p-5 m-6 ${colorClass} bg-opacity-40 cursor-pointer rounded-md backdrop-blur-sm`}
    >
      {children}
    </div>
  );
};

export default Panel;

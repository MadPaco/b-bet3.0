import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';
import { fetchTeamInfo } from '../../utility/api';
import { useAuth } from '../auth/AuthContext';
import { useState, useEffect } from 'react';

interface SidebarItemProps {
  icon: IconDefinition;
  text: string;
}

const SidebarItem: React.FC<SidebarItemProps> = ({ icon, text }) => {
  const { favTeam } = useAuth();
  const [primaryColor, setPrimaryColor] = useState('');

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setPrimaryColor(data.primaryColor))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

  const colorClasses = {
    red: 'bg-red-400 hover:bg-red-300',
    green: 'bg-green-400 hover:bg-green-300',
    blue: 'bg-blue-400 hover:bg-blue-300',
    gray: 'bg-gray-400 hover:bg-gray-300',
  };

  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <li
      className={`${colorClass} flex items-center px-5 py-1 ml-6 mt-3 bg-opacity-40 cursor-pointer rounded-md mx-3 backdrop-blur-sm`}
    >
      <div className="flex items-center">
        <div className="w-7 h-7 bg-transparent rounded-md flex items-center justify-center">
          <FontAwesomeIcon icon={icon} />
        </div>
      </div>
      <div className="ml-2">{text}</div>
    </li>
  );
};

export default SidebarItem;

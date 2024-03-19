import SidebarItem from './SidebarItem';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import { useColor } from '../../context/ColorContext';
import { useState } from 'react';
import { colorClasses } from '../../data/colorClasses';
import {
  faFootballBall,
  faRankingStar,
  faChartLine,
  faUsers,
  faGlobe,
  faCalendar,
  faHome,
  faUser,
  faBolt,
  faRightFromBracket,
  faBook,
  faUserShield,
} from '@fortawesome/free-solid-svg-icons';

interface SidebarProps {
  color: string;
}

const Sidebar: React.FC<SidebarProps> = () => {
  const { primaryColor } = useColor();
  const { roles } = useAuth();
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem('token');
    navigate('/login');
  };

  let sidebarItems = [
    {
      icon: faHome,
      text: 'Home',
      color: primaryColor,
      onClick: () => navigate('/'),
    },
    {
      icon: faFootballBall,
      text: 'Predictions',
      color: primaryColor,
      onClick: () => navigate('/predictions'),
    },
    {
      icon: faRankingStar,
      text: 'Leaderboard',
      color: primaryColor,
      onClick: () => navigate('/leaderboard'),
    },
    {
      icon: faGlobe,
      text: 'All Bets',
      color: primaryColor,
      onClick: () => navigate('/allBets'),
    },
    {
      icon: faChartLine,
      text: 'Stats',
      color: primaryColor,
      onClick: () => navigate('/stats'),
    },
    {
      icon: faCalendar,
      text: 'Schedule',
      color: primaryColor,
      onClick: () => navigate('/schedule'),
    },
    {
      icon: faBolt,
      text: '1 vs. 1',
      color: primaryColor,
      onClick: () => navigate('/1vs1'),
    },
    {
      icon: faUser,
      text: 'Profile',
      color: primaryColor,
      onClick: () => navigate('/myProfile'),
    },
    {
      icon: faUsers,
      text: 'Users',
      color: primaryColor,
      onClick: () => navigate('/allUsers'),
    },
    {
      icon: faBook,
      text: 'Rules',
      color: primaryColor,
      onClick: () => navigate('/rules'),
    },
    {
      icon: faRightFromBracket,
      text: 'Logout',
      onClick: handleLogout,
      color: primaryColor,
    },
  ];

  if (roles.includes('ADMIN')) {
    sidebarItems = [
      ...sidebarItems,
      {
        icon: faUserShield,
        text: 'Admin Panel',
        color: primaryColor,
        onClick: () => navigate('/admin'),
      },
    ];
  }

  const [isOpen, setIsOpen] = useState(false);

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <div
      className={`z-10 flex lg:bg-transparent bg-black sticky top-0 flex-row w-auto h-full text-gray-200 items-center lg:flex-col lg:h-screen lg:items-start`}
    >
      <button onClick={toggleDropdown} className="lg:hidden">
        {isOpen ? 'Close Menu' : 'Open Menu'}
      </button>
      <div
        className={`lg:flex ${isOpen ? 'flex' : 'hidden'} lg:block items-center justify-center h-10 border-b border-gray-700 lg:w-full lg:justify-start lg:border-b-0 lg:pl-4 lg:mg-3`}
      ></div>
      <ul
        className={`lg:flex ${isOpen ? 'flex' : 'hidden'} flex-row flex-wrap w-full h-full pt-3 pb-3 px-2 space-x-1 justify-center items-center lg:flex-col lg:justify-start lg:pr-0 lg:space-x-0 lg:space-y-3`}
      >
        {sidebarItems.map((item) => (
          <SidebarItem key={item.text} {...item} />
        ))}
      </ul>
    </div>
  );
};

export default Sidebar;

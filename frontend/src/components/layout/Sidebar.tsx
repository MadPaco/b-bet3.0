// src/components/Sidebar.tsx
import React, { useState } from 'react';
import SidebarItem from './SidebarItem';
import { useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
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
  faTrophy,
} from '@fortawesome/free-solid-svg-icons';

const Sidebar: React.FC = () => {
  const { roles } = useAuth();
  const navigate = useNavigate();
  const [isOpen, setIsOpen] = useState(false);
  const location = useLocation();

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('refresh_token');
    navigate('/login');
  };

  const toggleDropdown = () => {
    setIsOpen(!isOpen);
  };

  const sidebarItems = [
    { icon: faHome, text: 'Home', onClick: () => navigate('/dashboard'), path: '/dashboard' },
    { icon: faCalendar, text: 'Schedule', onClick: () => navigate('/schedule'), path: '/schedule' },
    { icon: faFootballBall, text: 'Predictions', onClick: () => navigate('/predictions'), path: '/predictions' },
    { icon: faGlobe, text: 'All Bets', onClick: () => navigate('/allBets'), path: '/allBets' },
    { icon: faRankingStar, text: 'Leaderboard', onClick: () => navigate('/leaderboard'), path: '/leaderboard' },
    { icon: faBolt, text: '1 vs. 1', onClick: () => navigate('/1vs1'), path: '/1vs1' },
    { icon: faTrophy, text: 'Achievements', onClick: () => navigate('/achievements'), path: '/achievements' },
    { icon: faChartLine, text: 'Stats', onClick: () => navigate('/stats'), path: '/stats' },
    { icon: faUsers, text: 'Users', onClick: () => navigate('/allUsers'), path: '/allUsers' },
    { icon: faUser, text: 'Edit Profile', onClick: () => navigate('/editProfile'), path: '/editProfile' },
    { icon: faBook, text: 'Rules', onClick: () => navigate('/rules'), path: '/rules' },
    { icon: faRightFromBracket, text: 'Logout', onClick: handleLogout, path: '/login' },
  ];

  if (roles.includes('ADMIN')) {
    sidebarItems.push({ icon: faUserShield, text: 'Admin Panel', onClick: () => navigate('/admin'), path: '/admin' });
  }

  return (
    <div className="sticky top-0 z-10 flex flex-col lg:h-screen lg:w-64 w-full bg-gray-900 text-white shadow-lg">
      <button onClick={toggleDropdown} className="lg:hidden p-4">
        {isOpen ? 'Close Menu' : 'Open Menu'}
      </button>
      <ul className={`lg:flex ${isOpen ? 'flex' : 'hidden'} flex-col w-full space-y-2 pt-3 pb-3`}>
        {sidebarItems.map((item) => (
          <SidebarItem
            key={item.text}
            icon={<FontAwesomeIcon icon={item.icon} />}
            text={item.text}
            onClick={item.onClick}
            active={location.pathname === item.path}
          />
        ))}
      </ul>
    </div>
  );
};

export default Sidebar;

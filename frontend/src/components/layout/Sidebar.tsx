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

  const { roles, username } = useAuth();
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
    { icon: faBolt, text: 'Preseason Predictions', onClick: () => navigate('/preseasonPredictions'), path: '/preseasonPredictions' },
    { icon: faTrophy, text: 'Achievements', onClick: () => navigate(`/users/${username}/achievements`), path: `/users/${username}/achievements` },
    { icon: faChartLine, text: 'Stats', onClick: () => navigate(`/users/${username}/stats`), path: `/users/${username}/stats` },
    { icon: faUsers, text: 'Users', onClick: () => navigate('/users/all'), path: '/users/all' },
    { icon: faUser, text: 'Edit Profile', onClick: () => navigate(`/users/${username}/profile/edit`), path: `/users/${username}/edit` },
    { icon: faBook, text: 'Rules, FAQ & Notes', onClick: () => navigate('/rules'), path: '/rules' },
    { icon: faRightFromBracket, text: 'Logout', onClick: handleLogout, path: '/login' },
  ];

  if (roles.includes('ADMIN')) {
    sidebarItems.push({ icon: faUserShield, text: 'Admin Panel', onClick: () => navigate('/admin'), path: '/admin' });
  }

  return (
    <div className="sticky top-0 z-10 flex flex-col lg:h-screen lg:w-40 lg:min-w-40 w-full bg-gray-900 text-white shadow-lg">
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

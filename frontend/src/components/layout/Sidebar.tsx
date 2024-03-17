import SidebarItem from './SidebarItem';
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
  faComments,
  faRightFromBracket,
  faBook,
} from '@fortawesome/free-solid-svg-icons';

const handleLogout = (): void => {
  localStorage.removeItem('token');
  window.location.href = '/';
};

interface SidebarProps {
  color: string;
}

const Sidebar: React.FC<SidebarProps> = ({ color }) => {
  const sidebarItems = [
    { icon: faHome, text: 'Home', color: color },
    { icon: faFootballBall, text: 'Predictions', color: color },
    { icon: faRankingStar, text: 'Leaderboard', color: color },
    { icon: faGlobe, text: 'All Bets', color: color },
    { icon: faChartLine, text: 'Stats', color: color },
    { icon: faCalendar, text: 'Schedule', color: color },
    { icon: faBolt, text: '1 vs. 1', color: color },
    { icon: faUser, text: 'Profile', color: color },
    { icon: faUsers, text: 'Users', color: color },
    { icon: faComments, text: 'Chat', color: color },
    { icon: faBook, text: 'Rules', color: color },
    {
      icon: faRightFromBracket,
      text: 'Logout',
      onClick: handleLogout,
      color: color,
    },
  ];

  return (
    <div className="flex flex-col w-55 h-screen bg-transparent border-b border-transparent col-span-1">
      <div className="flex flex-col h-screen w-full text-gray-200">
        <div className="flex items-center w-full justify-center h-14 border-b border-gray-700">
          <h1 className="text-xl font-bold">BBet</h1>
        </div>
        <ul>
          {sidebarItems.map((item) => (
            <SidebarItem key={item.text} {...item} />
          ))}
        </ul>
      </div>
    </div>
  );
};

export default Sidebar;

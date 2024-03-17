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

const Sidebar = () => {
  const sidebarItems = [
    { icon: faHome, text: 'Home' },
    { icon: faFootballBall, text: 'Predictions' },
    { icon: faRankingStar, text: 'Leaderboard' },
    { icon: faGlobe, text: 'All Bets' },
    { icon: faChartLine, text: 'Stats' },
    { icon: faCalendar, text: 'Schedule' },
    { icon: faBolt, text: '1 vs. 1' },
    { icon: faUser, text: 'Profile' },
    { icon: faUsers, text: 'Users' },
    { icon: faComments, text: 'Chat' },
    { icon: faBook, text: 'Rules' },
    { icon: faRightFromBracket, text: 'Logout', onClick: handleLogout },
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

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
    <div className="flex flex-row w-auto h-full text-gray-200 items-center lg:flex-col lg:h-screen lg:items-start">
      <div className=" hidden lg:block items-center justify-center h-14 border-b border-gray-700 lg:w-full lg:justify-start lg:border-b-0 lg:pl-4">
        <h1 className="text-xl font-bold">BBet</h1>
      </div>
      <ul className="flex flex-row flex-wrap w-full h-full pt-3 px-2 space-x-1 justify-center items-center lg:flex-col lg:justify-start lg:pr-0 lg:space-x-0 lg:space-y-3">
        {sidebarItems.map((item) => (
          <SidebarItem key={item.text} {...item} />
        ))}
      </ul>
    </div>
  );
};

export default Sidebar;

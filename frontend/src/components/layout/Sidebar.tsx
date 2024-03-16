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
  faComments,
} from '@fortawesome/free-solid-svg-icons';

const Sidebar = () => {
  return (
    <div className="flex flex-col w-55 h-screen bg-gray-800 border-b border-gray-700">
      <div className="flex flex-col h-screen w-full bg-blue-gray-800 text-gray-100">
        <div className="flex items-center w-full justify-center h-14 border-b border-gray-700">
          <h1 className="text-xl font-bold">BBet</h1>
        </div>
        <ul>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3 bg-gray-700">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faHome} />
              </div>
            </div>
            <div className="ml-2">Home</div>
          </li>
          <li className="px-5 py-4 text-sm italic text-center relative">
            Bets
            <div className="absolute left-0 right-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-gray-500 to-transparent"></div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faFootballBall} />
              </div>
            </div>
            <div className="ml-2">Predictions</div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faRankingStar} />
              </div>
            </div>
            <div className="ml-2">Standings</div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faGlobe} />
              </div>
            </div>
            <div className="ml-2">All Bets</div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faChartLine} />
              </div>
            </div>
            <div className="ml-2">Stats</div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faCalendar} />
              </div>
            </div>
            <div className="ml-2">Schedule</div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faBolt} />
              </div>
            </div>
            <div className="ml-2">1 vs. 1</div>
          </li>
          <li className="px-5 py-4 text-sm italic text-center relative">
            Social
            <div className="absolute left-0 right-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-gray-500 to-transparent"></div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faUser} />
              </div>
            </div>
            <div className="ml-2">My Profile</div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faUsers} />
              </div>
            </div>
            <div className="ml-2">Users</div>
          </li>
          <li className="flex items-center px-5 py-1 mt-3 hover:bg-gray-700 cursor-pointer rounded-md mx-3">
            <div className="flex items-center">
              <div className="w-7 h-7 bg-gray-500 rounded-md flex items-center justify-center">
                <FontAwesomeIcon icon={faComments} />
              </div>
            </div>
            <div className="ml-2">Chat</div>
          </li>
        </ul>
      </div>
    </div>
  );
};

export default Sidebar;

import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';

interface SidebarItemProps {
  icon: IconDefinition;
  text: string;
}

const SidebarItem: React.FC<SidebarItemProps> = ({ icon, text }) => (
  <li className="flex items-center px-5 py-1 ml-6 mt-3 hover:bg-gray-700 bg-gray-900 bg-opacity-60 cursor-pointer rounded-md mx-3 backdrop-blur-md">
    <div className="flex items-center">
      <div className="w-7 h-7 bg-transparent rounded-md flex items-center justify-center">
        <FontAwesomeIcon icon={icon} />
      </div>
    </div>
    <div className="ml-2">{text}</div>
  </li>
);

export default SidebarItem;

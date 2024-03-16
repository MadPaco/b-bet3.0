import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';

interface SidebarItemProps {
  icon: IconDefinition;
  text: string;
}

const SidebarItem: React.FC<SidebarItemProps> = ({ icon, text }) => (
  <li className="flex items-center px-5 py-1 ml-6 mt-3 hover:bg-teal-300 bg-teal-400 bg-opacity-40 cursor-pointer rounded-md mx-3 backdrop-blur-sm">
    <div className="flex items-center">
      <div className="w-7 h-7 bg-transparent rounded-md flex items-center justify-center">
        <FontAwesomeIcon icon={icon} />
      </div>
    </div>
    <div className="ml-2">{text}</div>
  </li>
);

export default SidebarItem;

import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';

interface SidebarItemProps {
  icon: IconDefinition;
  text: string;
  onClick?: () => void;
  color: string;
  active?: boolean;
}

const SidebarItem: React.FC<SidebarItemProps> = ({
  icon,
  text,
  onClick,
  color,
  active,
}) => {
  const activeClass = active ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-800';
  
  return (
    <li className="relative flex items-center">
      <button
        onClick={onClick}
        className={`${activeClass} flex items-center w-full p-3 rounded-md transition-colors duration-200 ease-in-out`}
      >
        <FontAwesomeIcon icon={icon} className="mr-3" />
        <span className="text-base">{text}</span>
      </button>
      {active && <div className="absolute right-0 top-0 w-2 h-full bg-blue-500 rounded-md"></div>}
    </li>
  );
};

export default SidebarItem;

import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';
import { colorClasses } from '../../data/colorClasses';

interface SidebarItemProps {
  icon: IconDefinition;
  text: string;
  onClick?: () => void;
  color: string;
  active?: boolean;
  path: string;
}

const SidebarItem: React.FC<SidebarItemProps> = ({
  icon,
  text,
  onClick,
  color,
  active,
}) => {
  const colorClass = color
    ? colorClasses[color as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <li className={`mt-1 lg:mt-0 lg:mx-0 lg:w-40 lg:h-10 `}>
      <button
        onClick={onClick}
        className={`${colorClass} ${active ? 'bg-gray-100' : ''} flex items-center px-1 py-1 bg-opacity-40 cursor-pointer rounded-md backdrop-blur-sm w-full lg:px-3 lg:ml-2`}
      >
        <div className="flex items-center lg:bg-gray-700 rounded-md">
          <div className="bg-transparent flex items-center justify-center h-10 w-10">
            <FontAwesomeIcon icon={icon} />
          </div>
        </div>
        <div className="hidden lg:block lg:ml-2">{text}</div>
      </button>
    </li>
  );
};

export default SidebarItem;

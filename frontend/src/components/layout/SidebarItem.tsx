import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/fontawesome-svg-core';
import { colorClasses } from '../../data/colorClasses';

interface SidebarItemProps {
  icon: IconDefinition;
  text: string;
  onClick?: () => void;
  color: string;
}

const SidebarItem: React.FC<SidebarItemProps> = ({
  icon,
  text,
  onClick,
  color,
}) => {
  const colorClass = color
    ? colorClasses[color as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <li className="mt-3 mx-8">
      <button
        onClick={onClick}
        className={`${colorClass} flex items-center px-5 py-1 ml-6 bg-opacity-40 cursor-pointer rounded-md backdrop-blur-sm w-full`}
      >
        <div className="flex items-center">
          <div className="w-7 h-7 bg-transparent rounded-md flex items-center justify-center">
            <FontAwesomeIcon icon={icon} />
          </div>
        </div>
        <div className="ml-2">{text}</div>
      </button>
    </li>
  );
};

export default SidebarItem;

import { colorClasses } from '../../data/colorClasses';

interface PanelProps {
  children: React.ReactNode;
  color: string;
}

const Panel: React.FC<PanelProps> = ({ children, color }) => {
  const colorClass = color
    ? colorClasses[color as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  return (
    <div
      className={`flex flex-col p-1 mx-4 mt-3 lg:p-5 lg:m-6 ${colorClass} bg-opacity-40 cursor-pointer rounded-md backdrop-blur-sm`}
    >
      {children}
    </div>
  );
};

export default Panel;

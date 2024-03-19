import { ReactNode } from 'react';
import Layout from './Layout';
import Sidebar from './Sidebar';
import { useColor } from '../../context/ColorContext';

interface LoggedInLayoutProps {
  children: ReactNode;
}

const LoggedInLayout: React.FC<LoggedInLayoutProps> = ({ children }) => {
  const { primaryColor } = useColor();
  return (
    <Layout
      content={
        <div className="flex flex-col lg:grid lg:grid-cols-7 w-full">
          <Sidebar color={primaryColor} />
          <div className="grid col-span-6 ">{children}</div>
        </div>
      }
    />
  );
};

export default LoggedInLayout;

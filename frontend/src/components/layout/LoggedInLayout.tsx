import { ReactNode } from 'react';
import Layout from './Layout';
import Sidebar from './Sidebar';
import { useAuth } from '../auth/AuthContext';
import { useEffect, useState } from 'react';
import { fetchTeamInfo } from '../../utility/api';

interface LoggedInLayoutProps {
  children: ReactNode;
}

const LoggedInLayout: React.FC<LoggedInLayoutProps> = ({ children }) => {
  const [primaryColor, setPrimaryColor] = useState<string>('gray');
  const { favTeam } = useAuth();

  useEffect(() => {
    if (favTeam) {
      fetchTeamInfo(favTeam)
        .then((data) => setPrimaryColor(data.primaryColor))
        .catch((error) => console.error(error));
    }
  }, [favTeam]);

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

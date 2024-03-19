import { ReactNode, useEffect } from 'react';
import Layout from './Layout';
import Sidebar from './Sidebar';
import { useColor } from '../../context/ColorContext';
import { useAuth } from '../auth/AuthContext';
import { useNavigate } from 'react-router-dom';

interface LoggedInLayoutProps {
  children: ReactNode;
}

const LoggedInLayout: React.FC<LoggedInLayoutProps> = ({ children }) => {
  const { primaryColor } = useColor();
  const { username } = useAuth();
  const navigate = useNavigate();
  useEffect(() => {
    if (!username) {
      navigate('/login');
    }
  }, [username, navigate]);

  return (
    <Layout
      content={
        <div className="flex flex-col lg:flex-row w-full">
          <Sidebar color={primaryColor} />
          <div className="flex-grow">{children}</div>
        </div>
      }
    />
  );
};

export default LoggedInLayout;

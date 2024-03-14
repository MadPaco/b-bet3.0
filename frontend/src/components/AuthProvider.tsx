import { useState, useEffect } from 'react';
import { jwtDecode, JwtPayload as DefaultJwtPayload } from 'jwt-decode';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from './AuthContext';

interface JwtPayload extends DefaultJwtPayload {
  username: string;
  roles: string[];
}

interface AuthProviderProps {
  children: React.ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [username, setUsername] = useState<string | null>(null);
  const [favTeam, setFavTeam] = useState<string | null>(null);
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        const decoded = jwtDecode<JwtPayload>(token);
        setUsername(decoded.username);
      } catch (error) {
        console.log(error);
        console.log(token);
        //localStorage.removeItem('token');
        navigate('/login');
      }
    } else {
      console.log('eat shit');
      navigate('/login');
    }
  }, [navigate]);

  return (
    <AuthContext.Provider
      value={{ username, setUsername, favTeam, setFavTeam }}
    >
      {children}
    </AuthContext.Provider>
  );
};

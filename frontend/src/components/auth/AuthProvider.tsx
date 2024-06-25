import { useState, useEffect } from 'react';
import { jwtDecode, JwtPayload as DefaultJwtPayload } from 'jwt-decode';
import { AuthContext } from './AuthContext';
import { useNavigate } from 'react-router-dom';
import { fetchUserInfo, fetchNewToken } from '../../utility/api';
import SyncLoader from 'react-spinners/SyncLoader';

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
  const [email, setEmail] = useState<string | null>(null);
  const [createdAt, setCreatedAt] = useState<Date | null>(null);
  const [roles, setRoles] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    const initializeAuth = async () => {
      const token = localStorage.getItem('token');
      const refreshToken = localStorage.getItem('refresh_token');

      if (!token) {
        setLoading(false);
        return;
      }

      try {
        const decoded = jwtDecode<JwtPayload>(token);
        const current_time = Date.now().valueOf() / 1000;

        if (decoded.exp && decoded.exp < current_time) {
          if (!refreshToken) {
            setLoading(false);
            navigate('/login');
            return;
          }

          try {
            await fetchNewToken();
          } catch (error) {
            setLoading(false);
            navigate('/login');
            return;
          }
        }

        const newToken = localStorage.getItem('token');
        if (newToken) {
          const newDecoded = jwtDecode<JwtPayload>(newToken);
          setUsername(newDecoded.username);
          setRoles(newDecoded.roles);

          const userData = await fetchUserInfo(newDecoded.username);
          setFavTeam(userData.favTeam);
          setEmail(userData.email);
          setCreatedAt(new Date(userData.createdAt));
        }
      } catch (error) {
        console.error('Error decoding token:', error);
      } finally {
        setLoading(false);
      }
    };

    initializeAuth();
  }, [navigate]);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-screen">
        <SyncLoader color="#36d7b7" />
      </div>
    );
  }

  return (
    <AuthContext.Provider
      value={{
        username,
        setUsername,
        favTeam,
        setFavTeam,
        email,
        setEmail,
        createdAt,
        setCreatedAt,
        roles,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

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
    const token = localStorage.getItem('token');
    const refreshToken = localStorage.getItem('refresh_token');
    if (token) {
      try {
        const decoded = jwtDecode<JwtPayload>(token);
        const current_time = Date.now().valueOf() / 1000;

        // check if the token has expired
        if (decoded.exp && decoded.exp < current_time) {
          // log the user out if no token and no refresh token are found
          if (!refreshToken) {
            setLoading(false);
            navigate('/login');
            return;
          }
          // else refresh the token
          else {
            fetchNewToken();
          }
        }
        setUsername(decoded.username);
        setRoles(decoded.roles);

        // Fetch user data from the backend
        const fetchUserData = async () => {
          try {
            const data = await fetchUserInfo(decoded.username);
            setFavTeam(data.favTeam);
            setEmail(data.email);
            setCreatedAt(new Date(data.createdAt));
            setLoading(false);
          } catch (error) {
            console.error('Error:', error);
            setLoading(false);
          }
        };
        //this is a dirty workaround to handle the case where the server is slightly ahead of the client
        //previously the client had to refresh twice to load the user data
        //if a new token has beeen issued
        //fix this later with adding a leeway
        setTimeout(fetchUserData, 200);
      } catch (error) {
        setLoading(false); // set loading to false if decoding the token fails
      }
    } else {
      setLoading(false); // set loading to false if there is no token
    }
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

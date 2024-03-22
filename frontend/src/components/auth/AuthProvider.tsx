import { useState, useEffect } from 'react';
import { jwtDecode, JwtPayload as DefaultJwtPayload } from 'jwt-decode';
import { AuthContext } from './AuthContext';
import { useNavigate } from 'react-router-dom';

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
            fetch('http://127.0.0.1:8000/api/token/refresh', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `refresh_token=${refreshToken}`,
            })
              .then((response) => {
                if (!response.ok) {
                  throw new Error('Network response was not ok');
                }
                return response.json();
              })
              .then((data) => {
                localStorage.setItem('token', data.token);
                setLoading(false);
              })
              .catch((error) => {
                console.error(
                  'There has been a problem with your fetch operation:',
                  error,
                );
                // localStorage.removeItem('token'); // remove the token from local storage
                // localStorage.removeItem('refresh_token'); // remove the refresh token from local storage
                // setLoading(false);
                // navigate('/login');
              });
          }
        }
        setUsername(decoded.username);
        setRoles(decoded.roles);

        // Fetch user data from the backend
        fetch(
          `http://127.0.0.1:8000/api/user/getUser/?username=${decoded.username}`,
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          },
        )
          .then((response) => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then((data) => {
            setFavTeam(data.favTeam);
            setEmail(data.email);
            setCreatedAt(new Date(data.createdAt));
            setLoading(false);
          })
          .catch((error) => {
            console.error(
              'There has been a problem with the fetch operation:',
              error,
            );
            setLoading(false); // set loading to false even if the fetch operation fails
          });
      } catch (error) {
        setLoading(false); // set loading to false if decoding the token fails
      }
    } else {
      setLoading(false); // set loading to false if there is no token
    }
  }, [navigate]);

  if (loading) {
    return <div>Loading...</div>;
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

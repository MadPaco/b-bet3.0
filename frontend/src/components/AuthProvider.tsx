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
  const [email, setEmail] = useState<string | null>(null);
  const [createdAt, setCreatedAt] = useState<Date | null>(null);

  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        const decoded = jwtDecode<JwtPayload>(token);
        setUsername(decoded.username);

        // Fetch user data from the backend
        fetch('http://127.0.0.1:8000/backend/user/', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then((data) => {
            console.log(data);
            setFavTeam(data.favTeam);
            setEmail(data.email);
            setCreatedAt(new Date(data.createdAt));
          })
          .catch((error) => {
            console.error(
              'There has been a problem with the fetch operation:',
              error,
            );
          });
      } catch (error) {
        console.log(error);
        console.log(token);
        navigate('/login');
      }
    } else {
      console.log('eat shit');
      navigate('/login');
    }
  }, [navigate]);

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
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

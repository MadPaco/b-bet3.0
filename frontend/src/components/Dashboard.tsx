import { jwtDecode, JwtPayload as DefaultJwtPayload } from 'jwt-decode';
import { useState, useEffect } from 'react';

const Dashboard: React.FC = () => {
  const [username, setUsername] = useState<string | null>(null);

  interface JwtPayload extends DefaultJwtPayload {
    username: string;
    roles: string[];
  }

  useEffect(() => {
    const token = localStorage.getItem('token');

    if (!token) {
      window.location.href = '/login';
    } else {
      try {
        const decodedToken = jwtDecode<JwtPayload>(token);
        if (!decodedToken || !decodedToken.username) {
          window.location.href = '/login';
        } else {
          setUsername(decodedToken.username);
        }
      } catch (error) {
        console.error('Invalid token', error);
        window.location.href = '/login';
      }
    }
  }, []);

  return (
    <div>
      <h1>{username}</h1>
    </div>
  );
};

export default Dashboard;

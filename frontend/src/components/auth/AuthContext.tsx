import { createContext, useContext, useState, useEffect } from 'react';

interface AuthContextProps {
  username: string | null;
  favTeam: string | null;
  setUsername: React.Dispatch<React.SetStateAction<string | null>>;
  setFavTeam: React.Dispatch<React.SetStateAction<string | null>>;
  email: string | null;
  setEmail: (email: string) => void;
  createdAt: Date | null;
  setCreatedAt: React.Dispatch<React.SetStateAction<Date | null>>;
  roles: string[];
  profilePicture: string | null;
  setProfilePicture: React.Dispatch<React.SetStateAction<string | null>>;
}

export const AuthContext = createContext<AuthContextProps | undefined>(
  undefined,
);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export const AuthProvider: React.FC = ({ children }) => {
  const [username, setUsername] = useState<string | null>(null);
  const [favTeam, setFavTeam] = useState<string | null>(null);
  const [email, setEmail] = useState<string | null>(null);
  const [createdAt, setCreatedAt] = useState<Date | null>(null);
  const [roles, setRoles] = useState<string[]>([]);
  const [profilePicture, setProfilePicture] = useState<string | null>(null);

  useEffect(() => {
    const fetchUserData = async () => {
      try {
        const response = await fetch(`/api/user/?username={username}`);
        const data = await response.json();
        setUsername(data.username);
        setFavTeam(data.favTeam);
        setEmail(data.email);
        setCreatedAt(new Date(data.createdAt));
        setRoles(data.roles);
        setProfilePicture(data.profilePicture);
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    };

    fetchUserData();
  }, []);

  return (
    <AuthContext.Provider
      value={{
        username,
        favTeam,
        setUsername,
        setFavTeam,
        email,
        setEmail,
        createdAt,
        setCreatedAt,
        roles,
        profilePicture,
        setProfilePicture,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

import { createContext, useContext } from 'react';

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

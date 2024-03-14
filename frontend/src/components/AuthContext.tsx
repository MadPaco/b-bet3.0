import { createContext, useContext } from 'react';

interface AuthContextProps {
  username: string | null;
  favTeam: string | null;
  setUsername: React.Dispatch<React.SetStateAction<string | null>>;
  setFavTeam: React.Dispatch<React.SetStateAction<string | null>>;
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

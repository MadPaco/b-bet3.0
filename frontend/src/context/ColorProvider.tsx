import { useState, useEffect } from 'react';
import { ReactNode } from 'react';
import { useAuth } from '../components/auth/AuthContext';
import { ColorContext } from './ColorContext';
import { fetchPrimaryColor } from '../utility/api';

interface ColorProviderProps {
  children: ReactNode;
}

export const ColorProvider: React.FC<ColorProviderProps> = ({ children }) => {
  const { favTeam } = useAuth();
  const [primaryColor, setPrimaryColor] = useState('gray');

  useEffect(() => {
    if (favTeam) {
      fetchPrimaryColor(favTeam).then(setPrimaryColor);
    }
  }, [favTeam]);

  return (
    <ColorContext.Provider value={{ primaryColor, setPrimaryColor }}>
      {children}
    </ColorContext.Provider>
  );
};

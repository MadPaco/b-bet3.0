import { createContext, useContext } from 'react';

type ColorContextType = {
  primaryColor: string;
  setPrimaryColor: (color: string) => void;
};

export const ColorContext = createContext<ColorContextType | undefined>(
  undefined,
);

export const useColor = () => {
  const context = useContext(ColorContext);
  if (!context) {
    throw new Error('useColor must be used within a ColorProvider');
  }
  return context;
};

export const ColorProvider = ColorContext.Provider;

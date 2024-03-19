module.exports = {
  transform: {
    '^.+\\.(ts|tsx)$': 'babel-jest',
  },
  testPathIgnorePatterns: ['/node_modules/', '\\.d\\.ts$'],
  testEnvironment: 'jsdom',
};

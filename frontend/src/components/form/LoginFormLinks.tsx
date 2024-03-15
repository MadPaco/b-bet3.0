import { useState } from 'react';

const LoginFormLinks = () => {
  const [rememberMe, setRememberMe] = useState(false);

  return (
    <div className="space-x-2">
      <label
        className="flex items-center mt-4 text-white"
        htmlFor="rememberMeCheckbox"
      >
        <input
          id="rememberMeCheckbox"
          type="checkbox"
          checked={rememberMe}
          onChange={(e) => setRememberMe(e.target.checked)}
        />
        <span className="ml-2">Remember me</span>
      </label>
      <a className="text-white hover:underline" href="/register">
        Register
      </a>
      <a className="text-white hover:underline" href="/forgot-password">
        Forgot Password?
      </a>
    </div>
  );
};

export default LoginFormLinks;

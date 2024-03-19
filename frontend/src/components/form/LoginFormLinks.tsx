const LoginFormLinks = () => {
  return (
    <div className="space-x-2">
      <label
        className="flex items-center mt-4 text-white"
        htmlFor="rememberMeCheckbox"
      ></label>
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

export async function fetchTeamInfo(team: string) {
  const token = localStorage.getItem('token');
  const response = await fetch(
    `http://127.0.0.1:8000/api/team/teaminfo/?favTeam=${team}`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    },
  );
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return await response.json();
}

//return the primary color of the team, or grey if no team is selected
export async function fetchPrimaryColor(team: string | null): Promise<string> {
  if (!team) {
    return 'gray';
  }

  const token = localStorage.getItem('token');
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/team/teaminfo/?favTeam=${team}`,
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      },
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    return data.primaryColor;
  } catch (error) {
    console.error(error);
    return 'gray';
  }
}

export async function fetchUserInfo(username: string) {
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/user/getUser/?username=${username}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      },
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const userInfo = await response.json();
    return userInfo;
  } catch (error) {
    console.error('Failed to fetch user info:', error);
    return null;
  }
}

export async function fetchAllUsers() {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/user/fetchAll`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const users = await response.json();
    return users;
  } catch (error) {
    console.error('Failed to fetch all users:', error);
    return null;
  }
}

export async function fetchAllTeamNames() {
  try {
    const response = await fetch(
      'http://127.0.0.1:8000/api/team/allTeams/names/',
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      },
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const teams = await response.json();
    return teams;
  } catch (error) {
    console.error('Failed to fetch all users:', error);
    return null;
  }
}

export async function fetchNewToken() {
  const refreshToken = localStorage.getItem('refresh_token');
  fetch('http://127.0.0.1:8000/api/token/refresh', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `refresh_token=${refreshToken}`,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then((data) => {
      localStorage.setItem('token', data.token);
    })
    .catch((error) => {
      console.error(
        'There has been a problem with your fetch operation:',
        error,
      );
      // localStorage.removeItem('token'); // remove the token from local storage
      // localStorage.removeItem('refresh_token'); // remove the refresh token from local storage
      // setLoading(false);
      // navigate('/login');
    });
}

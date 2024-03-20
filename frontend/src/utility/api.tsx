export async function fetchTeamInfo(team: string) {
  const token = localStorage.getItem('token');
  const response = await fetch(
    `http://127.0.0.1:8000/backend/team/?favTeam=${team}`,
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
      `http://127.0.0.1:8000/backend/team/?favTeam=${team}`,
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
      `http://127.0.0.1:8000/backend/user?username=${username}`,
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
    const response = await fetch(`http://127.0.0.1:8000/backend/fetchUsers`, {
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

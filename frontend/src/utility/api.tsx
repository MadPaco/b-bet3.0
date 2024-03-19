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

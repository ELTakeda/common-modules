import React, { useEffect, useState } from "react";

const NodeItem = () => (
  <div>Node item placeholder</div>
);

const NoData = () => (
  <div>No articles found.</div>
);

const NodeListOnly = () => {
  const [content, setContent] = useState(false);

  useEffect(() => {
    const user = "takeda"
    const pass ="Takeda.2023"
    const base64Credentials = btoa(`${user}:${pass}`)
    const url = `http://common-modules-demo.docksal.site`;

    const headers = new Headers({
      Authorization: `Basic ${base64Credentials}`,
      Accept: 'application/vnd.api+json',
    });

    fetch(url, headers)
      .then((response) => console.log(response.json()))
      .then((data) => setContent(data.data))
      .catch(err => console.log('There was an error accessing the API', err));
  }, []);

  return (
    <div>
      <h2>Site content</h2>
      {content ? (
        content.map((item) => <NodeItem key={item.id} {...item.attributes}/>)
      ) : (
        <NoData />
        )}
    </div>
  );
};

export default NodeListOnly;
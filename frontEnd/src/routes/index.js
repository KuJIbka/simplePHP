import React from 'react';
import { Route } from 'react-router-dom';

import Interface from 'container/Interface';
import Private from 'component/Private';

export default (
    <Route path="/" render={ ({match, location}) => (
        <Interface>
            <Route path="/" component={ Private } />
        </Interface>
    )} />
);
import React from 'react';
import { DataTable, TableHeader } from 'react-mdl';

const Users = ({ users }) => {

    return (
        <div style={{ display: 'inline-block', margin: 'auto', marginTop: '20px' }}>
            <DataTable
                shadow={0}
                rows={users}
            >
                <TableHeader numeric name="id">ID</TableHeader>
                <TableHeader name="username">Username</TableHeader>
                <TableHeader name="role">Role</TableHeader>
            </DataTable>
        </div>
    );
}

export default Users;
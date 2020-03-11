import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import Datetime from 'react-datetime'
import axios from 'axios'
import Alert from 'react-s-alert';
import SweetAlert from 'react-bootstrap-sweetalert';

// import { Container } from './styles';

export default class components extends Component {
  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      role_pekerjaan: "",
      nrbu: "-",
      id_personal: this.props.id_personal,
      isUpdate: false,
      delete: false
    }

  }

  render() {
    return(
      <div>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama Organisasi</th>
              <th>Jabatan</th>
              <th>Pekerjaan</th>
              <th>Tanggal</th>
              <th>Alamat</th>
              {!this.props.viewOnly && (
                <th colSpan={2}>Action</th>
              )}
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Nama_Badan_Usaha}</td>
                <td>{d.Jabatan}</td>
                <td>{d.Role_Pekerjaan}</td>
                <td>{d.Tgl_Mulai} - {d.Tgl_Selesai}</td>
                <td>{d.Alamat}</td>
                {!this.props.viewOnly && (<td><Button variant="outline-warning" size="sm" onClick={() => this.props.onUpdateClick(d)}><span className="cui-pencil"></span> Ubah</Button></td>)}
                {!this.props.viewOnly && (<td><Button variant="outline-danger" size="sm" onClick={() => this.props.onDeleteClick(d)}><span className="cui-trash"></span> Delete</Button></td>)}
              </tr>
            ))}
          </tbody>
        </Table>
      </div>
    )
  }
}

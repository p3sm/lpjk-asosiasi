import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table, Spinner } from 'react-bootstrap';
import Datetime from 'react-datetime'
import InputMask from 'react-input-mask';
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKabupaten from './MSelectKabupaten'
import axios from 'axios'
import Alert from 'react-s-alert';
import Moment from 'moment';

// import { Container } from './styles';

export default class InputBiodata extends Component {
  constructor(props){
    super(props)

    this.state = {
      submiting: false,
      showFormEdit: false,
      id_personal: this.props.data.No_KTP,
      nama: this.props.data.Nama,
      nama_tanpa_gelar: this.props.data.nama_tanpa_gelar,
      tempat_lahir: this.props.data.Tempat_Lahir,
      email: this.props.data.email,
      npwp: this.props.data.npwp,
      tgl_lahir: this.props.data.Tgl_Lahir,
      telepon: this.props.data.no_hp,
      jenis_kelamin: this.props.data.jenis_kelamin,
      negara: this.props.data.ID_Negara ? this.props.data.ID_Negara : "ID",
      provinsi: this.props.data.ID_Propinsi,
      kabupaten: this.props.data.ID_Kabupaten_Alamat,
      alamat: this.props.data.Alamat1,
      pos: this.props.data.Kodepos
    }
  }

  componentDidMount(){
  }

  render() {
    return (
      <div>
        <Table bordered>
          <tbody>
            <tr>
              <th>ID Personal</th>
              <td>{this.props.data.id_personal}</td>
              <th>NPWP</th>
              <td>{this.props.data.npwp}</td>
            </tr>
            <tr>
              <th>Nama Pemohon</th>
              <td>{this.props.data.Nama}</td>
              <th>Nama Tanpa Gelar</th>
              <td>{this.props.data.nama_tanpa_gelar}</td>
            </tr>
            <tr>
              <th>Tempat Lahir</th>
              <td>{this.props.data.Tempat_Lahir}</td>
              <th>Tanggal Lahir</th>
              <td>{Moment(this.props.data.Tgl_Lahir).format("DD-MM-YYYY")}</td>
            </tr>
            <tr>
              <th>Email</th>
              <td>{this.props.data.email}</td>
              <th>Telepon</th>
              <td>{this.props.data.no_hp}</td>
            </tr>
            <tr>
              <th>Jenis Kelamin</th>
              <td>{this.props.data.jenis_kelamin}</td>
              <th>Negara</th>
              <td>{this.props.data.ID_Negara}</td>
            </tr>
            <tr>
              <th>Provinsi</th>
              <td>{this.props.data.ID_Propinsi}</td>
              <th>Kabupaten</th>
              <td>{this.props.data.ID_Kabupaten_Alamat}</td>
            </tr>
            <tr>
              <th>Alamat</th>
              <td colSpan="3">{this.props.data.Alamat1}</td>
            </tr>
          </tbody>
        </Table>
      </div>
    );
  }
}

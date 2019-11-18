import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import moment from 'moment';
import Datetime from 'react-datetime'
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKualifikasi from './MSelectKualifikasi'
import MSelectBidang from './MSelectBidang'
import MSelectSubBidang from './MSelectSubBidang'
import MSelectUstk from './MSelectUstk'
import axios from 'axios'
import Alert from 'react-s-alert';

// import { Container } from './styles';

export default class components extends Component {
  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      id_personal: this.props.id_personal,
      tgl_registrasi: moment().format('YYYY-MM-DD'),
      me: null
    }

  }

  componentDidMount(){
    axios.get(`/api/user/me`).then(response => {
      console.log(response)
      this.setState({me: response.data})
    }).catch(err => {
      console.log(err)
    })
  }

  handleClose = () => {
    this.setState({showFormAdd: false})
  }

  onBidangChange = (data) => {
    this.setState({bidang: data.value})
    this.selectSubBidang.getSubBidang(data.value)
  }

  onUploadChangeHandler = event => {
    $( event.target ).siblings("label").addClass("selected")
    $( event.target ).siblings("label").append(" (" + event.target.files[0].name + ")")

    switch(event.target.id){
      case "file_berita_acara_vva":
        this.setState({ file_berita_acara_vva: event.target.files[0] })
        break;
      case "file_surat_permohonan_asosiasi":
        this.setState({ file_surat_permohonan_asosiasi: event.target.files[0] })
        break;
      case "file_surat_permohonan":
        this.setState({ file_surat_permohonan: event.target.files[0] })
        break;
      case "file_penilaian_mandiri":
        this.setState({ file_penilaian_mandiri: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  handleSubmit = () => {
    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("id_personal", this.state.id_personal);
    formData.append("sub_bidang", this.state.sub_bidang);
    formData.append("asosiasi", this.state.asosiasi);
    formData.append("kualifikasi", this.state.kualifikasi);
    formData.append("tgl_registrasi", this.state.tgl_registrasi);
    formData.append("provinsi", this.state.provinsi);
    formData.append("no_reg_asosiasi", this.state.no_reg_asosiasi);
    formData.append("id_unit_sertifikasi", this.state.id_unit_sertifikasi);
    formData.append("id_permohonan", this.state.id_permohonan);
    formData.append("file_berita_acara_vva", this.state.file_berita_acara_vva);
    formData.append("file_surat_permohonan_asosiasi", this.state.file_surat_permohonan_asosiasi);
    formData.append("file_surat_permohonan", this.state.file_surat_permohonan);
    formData.append("file_penilaian_mandiri", this.state.file_penilaian_mandiri);
    
    var endpoint = "/api/kualifikasi_ta/create"

    if(this.props.tipe_profesi !== 1)
      endpoint = "/api/kualifikasi_tt/create"
      
    axios.post(endpoint, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({submiting: false, showFormAdd: false})
      this.resetState()
      this.props.refreshData()
      
      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({submiting: false})
      Alert.error(err.response.data.message);
    })
  }

  resetState = () => {
    this.setState({
      id_personal: "",
      sub_bidang: "",
      asosiasi: "",
      kualifikasi: "",
      tgl_registrasi: "",
      provinsi: "",
      no_reg_asosiasi: "",
      id_unit_sertifikasi: "",
      id_permohonan: "",
      file_berita_acara_vva: "",
      file_surat_permohonan_asosiasi: "",
      file_surat_permohonan: "",
      file_penilaian_mandiri: ""
    })
  }

  render() {
    return(
      <div>
        <Button variant="outline-info" className="mb-3" onClick={() => this.setState({showFormAdd: true})}><span className="fa fa-edit"></span>Tambah Data</Button>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama</th>
              <th>Kualifikasi</th>
              <th>Sub Bidang</th>
              <th>Unit Sertifikasi</th>
              <th>Jenis Permohonan</th>
              <th>Asosiasi</th>
              <th>Provinsi</th>
              <th>Tanggal</th>
              <th>Status Terbaru</th>
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Nama}</td>
                <td>{d.ID_Kualifikasi}</td>
                <td>{d.ID_Sub_Bidang}</td>
                <td>{d.id_unit_sertifikasi}</td>
                <td>{d.id_permohonan}</td>
                <td>{d.ID_Asosiasi_Profesi}</td>
                <td>{d.ID_Propinsi_reg}</td>
                <td>{d.Tgl_Registrasi}</td>
                <td>{d.status_terbaru}</td>
              </tr>
            ))}
          </tbody>
        </Table>
        <Modal
        size="xl"
        onHide={this.handleClose}
        show={this.state.showFormAdd}>
          <Modal.Header closeButton>
            <Modal.Title>Tambah Data</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Form>
              <Row>
                <Col>
                  <Form.Group>
                    <Form.Label>Tanggal Permohonan</Form.Label>
                    <Form.Control disabled={true} value={this.state.tgl_registrasi}></Form.Control>
                  </Form.Group>

                  <MSelectKualifikasi tipe_profesi={this.props.tipe_profesi} value={this.state.kualifikasi} onChange={(data) => this.setState({kualifikasi: data.value})} />
                  
                  <MSelectBidang tipe_profesi={this.props.tipe_profesi} value={this.state.bidang} onChange={this.onBidangChange} />
                  
                  <MSelectSubBidang value={this.state.sub_bidang} onRef={ref => (this.selectSubBidang = ref)} onChange={(data) => this.setState({sub_bidang: data.value})} />

                  <Form.Group>
                    <Form.Label>Jenis Permohonan</Form.Label>
                    <Form.Control as="select" onChange={(e) => this.setState({id_permohonan: e.target.value})}>
                      <option value="">-- Pilih Jenis Permohonan --</option>
                      <option value="1">Baru</option>
                      <option value="2">Perpanjangan</option>
                      <option value="3">Perubahan</option>
                    </Form.Control>
                  </Form.Group>

                  <MSelectUstk value={this.state.id_unit_sertifikasi} provinsi_id={this.state.me ? this.state.me.asosiasi.provinsi_id : 0} onChange={(data) => this.setState({id_unit_sertifikasi: data.value})} />
                  
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>Asosiasi Profesi</Form.Label>
                    <Form.Control disabled={true} value={this.state.me ? this.state.me.asosiasi.detail.nama : ""}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Provinsi</Form.Label>
                    <Form.Control disabled={true} value={this.state.me ? this.state.me.asosiasi.provinsi.nama : ""}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>No Reg Asosiasi</Form.Label>
                    <Form.Control value={this.state.no_reg_asosiasi} onChange={(e) => this.setState({no_reg_asosiasi: e.target.value})}></Form.Control>
                  </Form.Group>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_berita_acara_vva" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_berita_acara_vva">Berita Acara VVA</label>
                  </div>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_surat_permohonan_asosiasi" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_surat_permohonan_asosiasi">Surat Pengantar Permohonan Asosiasi</label>
                  </div>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_surat_permohonan" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_surat_permohonan">Surat Permohonan</label>
                  </div>

                  {this.props.tipe_profesi === 1 && (
                    <div class="custom-file mb-3">
                      <input type="file" class="custom-file-input" id="file_penilaian_mandiri" onChange={this.onUploadChangeHandler}></input>
                      <label class="custom-file-label" for="file_penilaian_mandiri">Penilaian Mandiri Pemohon</label>
                    </div>
                  )}
                </Col>
              </Row>
            </Form>
          </Modal.Body>
          <Modal.Footer>
            <Button variant="light" onClick={this.handleClose}>
              Cancel
            </Button>
            <Button className="d-flex" disabled={this.state.submiting} variant="primary" onClick={!this.state.submiting ? this.handleSubmit : null}>
              {this.state.submiting ? 'Submiting...' : 'Submit'}
            </Button>
          </Modal.Footer>
          <Alert stack={{limit: 3}} position="top-right" offset="40" effect="slide" timeout="none" />
        </Modal>
      </div>
    )
  }
}

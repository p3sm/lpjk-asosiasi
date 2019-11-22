import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table, Spinner } from 'react-bootstrap';
import Datetime from 'react-datetime'
import InputMask from 'react-input-mask';
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKabupaten from './MSelectKabupaten'
import axios from 'axios'
import Alert from 'react-s-alert';
import Moment from 'react-moment';

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
      negara: this.props.data.ID_Negara,
      provinsi: this.props.data.ID_Propinsi,
      kabupaten: this.props.data.ID_Kabupaten_Alamat,
      alamat: this.props.data.Alamat1,
      pos: this.props.data.Kodepos
    }
  }

  componentDidMount(){
  }

  handleClose = () => {
    this.setState({showFormEdit: false})
  }

  onProvinsiChange = (data) => {
    this.setState({provinsi: data.value})
    this.selectKabupaten.getKabupaten(data.value)
  }

  onChangeHandler = event => {
    var size = event.target.files[0].size
    var label = $( event.target ).siblings("label")

    if(size > 20000000){
      Alert.error('Max file size 20mb')

      return
    }

    label.addClass("selected")
    label.html(event.target.files[0].name)

    switch(event.target.id){
      case "ktp":
        label.prepend("Upload KTP ")
        this.setState({ file_ktp: event.target.files[0] })
        break;
      case "npwp":
        label.prepend("Upload NPWP ")
        this.setState({ file_npwp: event.target.files[0] })
        break;
      case "cv":
        label.prepend("Upload Daftar Riwayat Hidup ")
        this.setState({ file_cv: event.target.files[0] })
        break;
      case "pernyataan":
        label.prepend("Upload Surat Pernyataan Kebenaran Data Pemohon ")
        this.setState({ file_pernyataan: event.target.files[0] })
        break;
      case "photo":
        label.prepend("Upload Pas Photo Pemohon ")
        this.setState({ file_photo: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  handleSubmit = () => {
    console.log(this.state.id_personal)
    if(this.state.id_personal.length != 16){
      Alert.error('ID Personal / KTP harus 16 karakter')

      return
    }

    if(!/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[A-Za-z]+$/.test(this.state.email)){
      Alert.error('Format email tidak valid')

      return
    }

    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("jenis_tenaga_kerja", this.props.tipe_profesi === 1 ? "tenaga_ahli" : "tenaga_terampil");
    formData.append("id_personal", this.state.id_personal);
    formData.append("nama", this.state.nama);
    formData.append("nama_tanpa_gelar", this.state.nama_tanpa_gelar);
    formData.append("tempat_lahir", this.state.tempat_lahir);
    formData.append("email", this.state.email);
    formData.append("npwp",  this.props.tipe_profesi === 2 && this.state.npwp == "" ? "-" : this.state.npwp);
    formData.append("tgl_lahir", this.state.tgl_lahir);
    formData.append("telepon", this.state.telepon);
    formData.append("jenis_kelamin", this.state.jenis_kelamin);
    formData.append("negara", this.state.negara);
    formData.append("provinsi", this.state.provinsi);
    formData.append("kabupaten", this.state.kabupaten);
    formData.append("alamat", this.state.alamat);
    formData.append("pos", this.state.pos);
    formData.append("file_ktp", this.state.file_ktp);
    formData.append("file_npwp", this.state.file_npwp);
    formData.append("file_cv", this.state.file_cv);
    formData.append("file_pernyataan", this.state.file_pernyataan);
    formData.append("file_photo", this.state.file_photo);

    axios.post(`/api/biodata/` + this.state.id_personal, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({submiting: false, showFormEdit: false})
      this.props.refreshData()
      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({submiting: false})
      Alert.error(err.response.data.message);
    })
  }

  render() {
    return (
      <div>
        <Button variant="outline-info" className="mb-3" onClick={() => this.setState({showFormEdit: true})}><span className="fa fa-edit"></span>Edit Data</Button>
        <Table bordered>
          <tbody>
            <tr>
              <th>ID Personal</th>
              <td>{this.props.data.id_personal}</td>
              <th>NPWP</th>
              <td>{this.props.data.npwp}</td>
            </tr>
            <tr>
              <th>Nama Sertifikat</th>
              <td>{this.props.data.Nama}</td>
              <th>Nama Tanpa Gelar</th>
              <td>{this.props.data.nama_tanpa_gelar}</td>
            </tr>
            <tr>
              <th>Tempat Lahir</th>
              <td>{this.props.data.Tempat_Lahir}</td>
              <th>Tanggal Lahir</th>
              <td>{this.props.data.Tgl_Lahir}</td>
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
            {/* <tr>
              <th>KTP</th>
              <td><a data-type="iframe" data-fancybox href={this.props.data.file ? this.props.data.file["persyaratan_5"] : "#"}>View</a></td>
              <th>NPWP</th>
              <td><a data-fancybox href={this.props.data.file ? this.props.data.file["persyaratan_8"] : "#"}>View</a></td>
            </tr>
            <tr>
              <th>Pernyataan Kebenaran Data</th>
              <td><a data-fancybox href={this.props.data.file ? this.props.data.file["persyaratan_4"] : "#"}>View</a></td>
              <th>Daftar Riwayat Hidup</th>
              <td><a data-fancybox href={this.props.data.file ? this.props.data.file["persyaratan_11"] : "#"}>View</a></td>
            </tr> */}
          </tbody>
        </Table>
        <Modal
        size="xl"
        onHide={this.handleClose}
        show={this.state.showFormEdit}>
          <Modal.Header closeButton>
            <Modal.Title>Ubah Data</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Form>
              <Row>
                <Col>
                  <Form.Group>
                    <Form.Label>Nama Sertifikat</Form.Label>
                    <Form.Control id="nama" name="nama" onChange={(e) => this.setState({nama: e.target.value})} placeholder="" value={this.state.nama}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Tempat Lahir</Form.Label>
                    <Form.Control type="text" id="tempat_lahir" name="tempat_lahir" onChange={(e) => this.setState({tempat_lahir: e.target.value})} placeholder="" value={this.state.tempat_lahir}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Email</Form.Label>
                    <Form.Control type="email" id="email" name="email" onChange={(e) => this.setState({email: e.target.value})} placeholder="" value={this.state.email}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>NPWP</Form.Label>
                    <InputMask mask="99.999.999.9-999.999" type="text" id="npwp" name="npwp" onChange={(e) => this.setState({npwp: e.target.value})} placeholder="" value={this.state.npwp}>
                      {(inputProps) => <Form.Control {...inputProps}></Form.Control>}
                    </InputMask>
                  </Form.Group>
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>Nama Tanpa Gelar</Form.Label>
                    <Form.Control type="text" id="nama_tanpa_gelar" name="nama_tanpa_gelar" onChange={(e) => this.setState({nama_tanpa_gelar: e.target.value})} placeholder="" value={this.state.nama_tanpa_gelar}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Tanggal Lahir</Form.Label>
                    <Datetime closeOnSelect={true} inputProps={{ placeholder: 'contoh: 1980-01-01'}} value={this.state.tgl_lahir} dateFormat="YYYY-MM-DD" onChange={(e) => {
                      try {
                        this.setState({tgl_lahir: e.format("YYYY-MM-DD")})
                      } catch (err) {
                        this.setState({tgl_lahir: e})
                      }
                    }} timeFormat={false} />
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Telepon</Form.Label>
                    <Form.Control type="number" id="telepon" name="telepon" onChange={(e) => this.setState({telepon: e.target.value})} placeholder="" value={this.state.telepon}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Jenis Kelamin</Form.Label>
                    <Form.Control as="select" name="jenis_kelamin" onChange={(e) => this.setState({jenis_kelamin: e.target.value})}>
                      <option>-- pilih jenis kelamin --</option>
                      <option selected={this.state.jenis_kelamin == "L" ? "selected" : ""} value="L">Pria</option>
                      <option selected={this.state.jenis_kelamin == "P" ? "selected" : ""} value="P">Wanita</option>
                    </Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Negara</Form.Label>
                    <Form.Control as="select" name="negara" onChange={(e) => this.setState({negara: e.target.value})}>
                      <option>Indonesia</option>
                    </Form.Control>
                  </Form.Group>
                </Col>
              </Row>
              <Card>
                <Card.Header>
                  Alamat Sesuai KTP
                </Card.Header>
                <Card.Body>
                  <Row>
                    <Col md>
                      <MSelectProvinsi value={this.state.provinsi} onChange={(data) => this.onProvinsiChange(data)} />
                      <MSelectKabupaten provinsiId={this.state.provinsi} value={this.state.kabupaten} onRef={ref => (this.selectKabupaten = ref)} onChange={(data) => this.setState({kabupaten: data.value})} />
                      <Form.Group>
                        <Form.Label>Alamat</Form.Label>
                        <Form.Control as="textarea" id="alamat" name="alamat" row="3" value={this.state.alamat} onChange={(e) => this.setState({alamat: e.target.value})}></Form.Control>
                      </Form.Group>
                      <Form.Group>
                        <Form.Label>Kode Pos</Form.Label>
                        <Form.Control type="text" className="form-control" id="pos" name="pos" onChange={(e) => this.setState({pos: e.target.value})} value={this.state.pos} placeholder=""></Form.Control>
                      </Form.Group>
                    </Col>
                    <Col md>
                      <div className="custom-file mb-3">
                        <input type="file" className="custom-file-input" id="ktp" onChange={this.onChangeHandler}></input>
                        <label className="custom-file-label" htmlFor="ktp">Upload KTP</label>
                      </div>
                      {this.props.tipe_profesi === 1 && (
                          <div className="custom-file mb-3 form-group">
                            <input type="file" className="custom-file-input" id="npwp" onChange={this.onChangeHandler}></input>
                            <label className="custom-file-label" htmlFor="npwp">Upload NPWP</label>
                          </div>
                        )
                      }
                      <div className="custom-file mb-3">
                        <input type="file" className="custom-file-input" id="cv" onChange={this.onChangeHandler}></input>
                        <label className="custom-file-label" htmlFor="cv">Upload Daftar Riwayat Hidup</label>
                      </div>
                      <div className="custom-file mb-3">
                        <input type="file" className="custom-file-input" id="pernyataan" onChange={this.onChangeHandler}></input>
                        <label className="custom-file-label" htmlFor="pernyataan">Upload Surat Pernyataan Kebenaran Data Pemohon</label>
                      </div>
                      <div className="custom-file mb-3">
                        <input type="file" className="custom-file-input" id="photo" onChange={this.onChangeHandler}></input>
                        <label className="custom-file-label" htmlFor="photo">Upload Pas Photo Pemohon</label>
                      </div>
                    </Col>
                  </Row>
                </Card.Body>
              </Card>
            </Form>
          </Modal.Body>
          <Modal.Footer>
            <Button variant="light" onClick={this.handleClose}>
              Cancel
            </Button>
            <Button className="d-flex" disabled={this.state.submiting} variant="primary" onClick={!this.state.submiting ? this.handleSubmit : null}>
              {this.state.submiting ? 'Updating...' : 'Update'}
            </Button>
          </Modal.Footer>
          <Alert stack={{limit: 3}} position="top-right" offset="40" effect="slide" timeout="none" />
        </Modal>
      </div>
    );
  }
}

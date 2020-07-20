import React, { Component } from "react";
import { Form, Button, Row, Col, Card, Modal, Table } from "react-bootstrap";
import moment from "moment";
import Datetime from "react-datetime";
import MSelectProvinsi from "./MSelectProvinsi";
import MSelectKualifikasi from "./MSelectKualifikasi";
import MSelectBidang from "./MSelectBidang";
import MSelectSubBidang from "./MSelectSubBidang";
import MSelectUstk from "./MSelectUstk";
import axios from "axios";
import Alert from "react-s-alert";
import SweetAlert from "react-bootstrap-sweetalert";
import swal from "sweetalert";

// import { Container } from './styles';

export default class components extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showFormAdd: false,
            pengajuan: false,
            submiting: [],
            diajukan: [],
            id_personal: this.props.id_personal,
            id_permohonan: "1",
            tgl_registrasi: moment().format("YYYY-MM-DD"),
            no_reg_asosiasi: "",
            me: null,
            delete: false
        };
    }

    kirimPengajuan = () => {
        var submiting = this.state.submiting;
        var diajukan = this.state.diajukan;
        submiting[this.state.submit_index] = true;

        this.setState({ submiting: submiting, pengajuan: false });

        var formData = new FormData();
        formData.append("permohonan_id", this.state.submit_id_permohonan);
        formData.append("tanggal", this.state.submit_tanggal);
        formData.append("id_personal", this.state.submit_id_personal);

        let uri =
            this.props.tipe_profesi == 1
                ? "/api/kualifikasi_ta/naik_status"
                : "/api/kualifikasi_tt/naik_status";

        axios
            .post(uri, formData)
            .then(response => {
                console.log(response);

                submiting[this.state.submit_index] = false;
                diajukan[this.state.submit_index] = true;
                this.setState({ submiting: submiting, diajukan: diajukan });
                //   this.props.refreshData()

                //   Alert.success(response.data.message);
            })
            .catch(err => {
                console.log(err);

                submiting[this.state.submit_index] = false;
                this.setState({ submiting: submiting });

                swal({
                    text: err,
                    icon: "error"
                });
            });
    };

    confirmPengajuan = (index, id_permohonan, tanggal, id_personal) => {
        this.setState({
            pengajuan: true,
            submit_index: index,
            submit_id_permohonan: id_permohonan,
            submit_tanggal: tanggal,
            submit_id_personal: id_personal
        });
    };

    render() {
        return (
            <div>
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
                            <th>Status Terakhir</th>
                            <th>Dokumen</th>
                            <th>Naik Status</th>
                        </tr>
                        {this.props.data.map((d, i) => (
                            <tr>
                                <td>{d.Nama}</td>
                                <td>{d.ID_Kualifikasi}</td>
                                <td>{d.ID_Sub_Bidang}</td>
                                <td>{d.id_unit_sertifikasi}</td>
                                <td>
                                    {d.id_permohonan == 1
                                        ? "Baru"
                                        : d.id_permohonan == 2
                                        ? "Perpanjangan"
                                        : "Perubahan"}
                                </td>
                                <td>{d.ID_Asosiasi_Profesi}</td>
                                <td>
                                    {this.props.tipe_profesi == 1
                                        ? d.ID_Propinsi_reg
                                        : d.ID_propinsi_reg}
                                </td>
                                <td>{d.Tgl_Registrasi}</td>
                                <td>{d.status_terbaru}</td>
                                <td>
                                    <a
                                        className="fancybox"
                                        data-fancybox
                                        data-type="iframe"
                                        data-src={
                                            "/document?profesi= " +
                                            this.props.tipe_profesi +
                                            "&data=" +
                                            d.doc_url
                                        }
                                        href="javascript:;"
                                    >
                                        View
                                    </a>
                                </td>
                                <td className="text-center">
                                    {d.status_terbaru == "99" &&
                                        d.diajukan != "1" &&
                                        !this.state.diajukan[i] &&
                                        !this.state.submiting[i] && (
                                            <Button
                                                variant="outline-success"
                                                size="sm"
                                                onClick={() =>
                                                    this.confirmPengajuan(
                                                        i,
                                                        this.props
                                                            .tipe_profesi == 1
                                                            ? d.ID_Registrasi_TK_Ahli
                                                            : d.ID_Registrasi_TK_Trampil,
                                                        d.Tgl_Registrasi,
                                                        d.ID_Personal
                                                    )
                                                }
                                            >
                                                Ajukan
                                            </Button>
                                        )}
                                    {((d.diajukan == "1" &&
                                        d.status_terbaru == "99") ||
                                        this.state.diajukan[i]) && (
                                        <span className="badge badge-success">
                                            Sudah diajukan
                                        </span>
                                    )}
                                    {this.state.submiting[i] && (
                                        <div
                                            class="spinner-border spinner-border-sm text-success"
                                            role="status"
                                        >
                                            <span class="sr-only">
                                                Loading...
                                            </span>
                                        </div>
                                    )}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </Table>

                <SweetAlert
                    show={this.state.pengajuan}
                    warning
                    showCancel
                    title="Kirim Pengajuan"
                    btnSize="md"
                    confirmBtnBsStyle="success"
                    cancelBtnText="Close"
                    confirmBtnText={
                        this.state.deleting ? "Submiting..." : "Ya, Saya yakin"
                    }
                    onConfirm={() => this.kirimPengajuan()}
                    onCancel={() => this.setState({ pengajuan: false })}
                >
                    Saya yakin data terisi dengan sebenar-benarnya
                </SweetAlert>
            </div>
        );
    }
}
